import { createApp } from 'vue';
import VerificationTable from './components/tables/VerificationTable.vue';

const app = createApp({
    components: {
        VerificationTable
    }
});

app.mount('#verification-table-app');
