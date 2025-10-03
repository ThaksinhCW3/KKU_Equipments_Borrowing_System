<div class="">
    <div id="filter"
         data-categories='@json(($categories ?? []))'
         data-current-category='{{ request('category','') }}'
         data-current-availability='{{ request('availability','') }}'
         data-current-name='{{ request('name','') }}'>
    </div>
</div>

