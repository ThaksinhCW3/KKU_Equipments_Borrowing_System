<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\BorrowRequest;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MultiFilteredExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $type;

    public function __construct(Request $request, string $type)
    {
        $this->request = $request;
        $this->type = $type;
    }

    public function collection()
    {
        return match ($this->type) {
            'users' => $this->filteredUsers(),
            'equipments' => $this->filteredEquipments(),
            'categories' => $this->filteredCategories(),
            'requests' => $this->filteredRequests(),
            'transactions' => $this->filteredTransactions(),
            default => collect(),
        };
    }

    public function headings(): array
    {
        return match ($this->type) {
            'users' => ['ไอดี', 'รหัสผู้ใช้', 'ชื่อ-นามสกุน', 'อีเมล', 'โทรศัพท์', 'ตำแหน่ง', 'สร้างวันที่'],
            'equipments' => ['ไอดี', 'หมายเลขคุรุภัณฑ์', 'ชื่ออุปกรณ์', 'หมวดหมู่', 'สถานะ', 'สร้างวันที่'],
            'categories' => ['ไอดี', 'รหัสหมวดหมู่', 'ชื่อ-นามสกุน', 'สร้างวันที่'],
            'requests' => ['ไอดี', 'รหัสคำขอ', 'ไอดีผู้ยืม', 'ชื่อผู้ยืม', 'เลขไอดีอุปกรณ์', 'ชื่ออุปกรณ์', 'เริ่มวันที่', 'ถึงวันที่', 'สถานะ', 'สาเหตุการปฏิเสธ', 'สาเหตุการยกเลิก', 'สร้างวันที่'],
            'transactions' => ['ไอดี', 'รหัสธุรกรรม', 'ประเภทธุรกรรม', 'ชื่อผู้ใช้', 'ชื่ออุปกรณ์', 'สถานะ', 'จำนวนเงิน', 'เริ่มวันที่', 'ถึงวันที่', 'สร้างวันที่'],
            default => [],
        };
    }

    protected function filteredUsers()
    {
        $query = User::query();

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phonenumber', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        if ($this->request->filled('role')) {
            $query->where('role', $this->request->role);
        }

        if ($this->request->filled('sort')) {
            $query->orderBy($this->request->sort, $this->request->direction ?? 'asc');
        }

        return $query->get()->map(fn($u) => [
            $u->id,
            $u->uid,
            $u->name,
            $u->email,
            $u->phonenumber,
            ucfirst($u->role),
            optional($u->created_at)->format('d-m-Y'),
        ]);
    }

    protected function filteredEquipments()
    {
        $query = Equipment::with('category');

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($this->request->filled('category_id')) {
            $query->where('categories_id', $this->request->category_id);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('sort')) {
            $query->orderBy($this->request->sort, $this->request->direction ?? 'asc');
        }

        return $query->get()->map(fn($eq) => [
            $eq->id,
            $eq->code,
            $eq->name,
            $eq->category->name ?? 'N/A',
            ucfirst($eq->status),
            optional($eq->created_at)->format('d-m-Y'),
        ]);
    }
    protected function filteredCategories()
    {
        $query = Category::withCount('equipments');

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('cate_id', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['id', 'cate_id', 'name', 'created_at', 'updated_at', 'equipments_count'];
        $sort = in_array($this->request->sort, $allowedSorts) ? $this->request->sort : 'name';
        $direction = $this->request->direction ?? 'asc';

        $query->orderBy($sort, $direction);

        return $query->get()->map(fn($c) => [
            $c->id,
            $c->cate_id,
            $c->name,
            $c->equipments_count,
            optional($c->created_at)->format('d-m-Y'),
            optional($c->updated_at)->format('d-m-Y'),
        ]);
    }
    protected function filteredRequests()
    {
        $query = BorrowRequest::with('user', 'equipment');

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('uid', 'like', "%{$search}%")
                )->orWhereHas(
                    'equipment',
                    fn($e) =>
                    $e->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                );
            });
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('sort')) {
            $query->orderBy($this->request->sort, $this->request->direction ?? 'asc');
        }

        return $query->get()->map(fn($r) => [
            $r->id,
            $r->req_id,
            $r->user->uid,
            $r->user->name,
            $r->equipment->code,
            $r->equipment->name,
            $r->start_at ? $r->start_at->format('d-m-Y') : '-',
            $r->end_at ? $r->end_at->format('d-m-Y') : '-',
            $r->status,
            $r->reject_reason ?? '-',
            $r->$r->cancel_reason ?? '-',
            optional($r->created_at)->format('d-m-Y'),
        ]);
    }

    protected function filteredTransactions()
    {
        // Generate transaction data from borrow requests (same logic as in ReportController)
        $requests = BorrowRequest::with('user', 'equipment')->get();
        
        $transactions = collect();
        
        // Generate transactions from borrow requests
        foreach ($requests as $request) {
            // Borrow transaction
            $transactions->push([
                'id' => $request->id * 1000 + 1,
                'transaction_id' => 'TXN' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . 'B',
                'transaction_type' => 'borrow',
                'user' => $request->user,
                'equipment' => $request->equipment,
                'status' => $request->status === 'approved' ? 'completed' : 
                           ($request->status === 'rejected' ? 'cancelled' : 'pending'),
                'amount' => 0, // Free borrowing
                'start_date' => $request->start_at,
                'end_date' => $request->end_at,
                'description' => 'การยืมอุปกรณ์: ' . ($request->equipment->name ?? 'N/A'),
                'notes' => $request->request_reason,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at
            ]);
            
            // Return transaction (if status is check_in)
            if ($request->status === 'check_in') {
                $transactions->push([
                    'id' => $request->id * 1000 + 2,
                    'transaction_id' => 'TXN' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . 'R',
                    'transaction_type' => 'return',
                    'user' => $request->user,
                    'equipment' => $request->equipment,
                    'status' => 'completed',
                    'amount' => 0,
                    'start_date' => $request->end_at,
                    'end_date' => $request->end_at,
                    'description' => 'การคืนอุปกรณ์: ' . ($request->equipment->name ?? 'N/A'),
                    'notes' => 'คืนอุปกรณ์ตามกำหนด',
                    'created_at' => $request->updated_at,
                    'updated_at' => $request->updated_at
                ]);
            }
            
            // Penalty transaction (if late return)
            if ($request->status === 'check_in' && $request->end_at && $request->updated_at) {
                $endDate = \Carbon\Carbon::parse($request->end_at);
                $returnDate = \Carbon\Carbon::parse($request->updated_at);
                
                if ($returnDate->gt($endDate)) {
                    $daysLate = $returnDate->diffInDays($endDate);
                    $penaltyAmount = $daysLate * 50; // 50 baht per day
                    
                    $transactions->push([
                        'id' => $request->id * 1000 + 3,
                        'transaction_id' => 'TXN' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . 'P',
                        'transaction_type' => 'penalty',
                        'user' => $request->user,
                        'equipment' => $request->equipment,
                        'status' => 'completed',
                        'amount' => $penaltyAmount,
                        'start_date' => $request->end_at,
                        'end_date' => $request->updated_at,
                        'description' => 'ค่าปรับการคืนล่าช้า: ' . ($request->equipment->name ?? 'N/A') . ' (' . $daysLate . ' วัน)',
                        'notes' => 'คืนล่าช้า ' . $daysLate . ' วัน',
                        'created_at' => $request->updated_at,
                        'updated_at' => $request->updated_at
                    ]);
                }
            }
        }
        
        // Apply filters
        $filteredTransactions = $transactions->filter(function ($transaction) {
            if ($this->request->transaction_type && $transaction['transaction_type'] !== $this->request->transaction_type) {
                return false;
            }
            
            if ($this->request->status && $transaction['status'] !== $this->request->status) {
                return false;
            }
            
            if ($this->request->user_name && !str_contains(strtolower($transaction['user']['name'] ?? ''), strtolower($this->request->user_name))) {
                return false;
            }
            
            if ($this->request->equipment_name && !str_contains(strtolower($transaction['equipment']['name'] ?? ''), strtolower($this->request->equipment_name))) {
                return false;
            }
            
            if ($this->request->date_from && $transaction['created_at'] < $this->request->date_from) {
                return false;
            }
            
            if ($this->request->date_to && $transaction['created_at'] > $this->request->date_to) {
                return false;
            }
            
            return true;
        });
        
        return $filteredTransactions->map(function ($transaction) {
            return [
                $transaction['id'],
                $transaction['transaction_id'],
                $transaction['transaction_type'],
                $transaction['user']['name'] ?? 'N/A',
                $transaction['equipment']['name'] ?? 'N/A',
                $transaction['status'],
                $transaction['amount'],
                $transaction['start_date'] ? \Carbon\Carbon::parse($transaction['start_date'])->format('d-m-Y') : '-',
                $transaction['end_date'] ? \Carbon\Carbon::parse($transaction['end_date'])->format('d-m-Y') : '-',
                $transaction['created_at'] ? \Carbon\Carbon::parse($transaction['created_at'])->format('d-m-Y') : '-',
            ];
        });
    }
}
