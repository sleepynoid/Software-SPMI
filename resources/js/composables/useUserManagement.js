import { ref, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue';

export function useUserManagement() {
  const users = ref([]);
  const showModal = ref(false);
  const submitted = ref(false);
  const menu = ref();
  const toast = useToast();

  const newUser = reactive({
    email: '',
    name: '',
    role: '',
    password: '',
  });

  const roles = [
    { name: 'Evaluasi', value: 'Evaluasi' },
    { name: 'Pelaksanaan', value: 'Pelaksanaan' },
    { name: 'Peningkatan', value: 'Peningkatan' },
    { name: 'Pengendalian', value: 'Pengendalian' },
    { name: 'Admin', value: 'Admin' },
  ];

  const showEditModal = ref(false);
  const selectedUser = ref(null);
  const editSubmitted = ref(false);

  const showResetModal = ref(false);
  const newPassword = ref('');
  const resetSubmitted = ref(false);

  const showHistoryModal = ref(false);
  const userHistory = ref([]);
  const isLoadingHistory = ref(false);
  const historyError = ref(null);

  const fetchUsers = async () => {
    try {
      const response = await fetch('/admin/users', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      users.value = await response.json();
    } catch (error) {
      console.error('Failed to fetch users:', error);
    }
  };

  const registerUser = () => {
    submitted.value = true;
    if (!newUser.email || !newUser.name || !newUser.role || !newUser.password) return;

    router.post('/admin/users', { ...newUser }, {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({ severity: 'success', summary: 'Success', detail: 'User registered successfully', life: 3000 });
        showModal.value = false;
        fetchUsers();
        resetForm();
      },
      onError: (errors) => {
        const msg = Object.values(errors).join(', ');
        toast.add({ severity: 'error', summary: 'Error', detail: msg, life: 5000 });
      },
    });
  };

  const resetForm = () => {
    submitted.value = false;
    Object.assign(newUser, { email: '', name: '', role: '', password: '' });
  };

  const closeModal = () => {
    showModal.value = false;
    resetForm();
  };

  const updateUserRole = () => {
    editSubmitted.value = true;
    if (!selectedUser.value?.role) return;

    router.post('/admin/users/role', {
      user_id: selectedUser.value.id,
      new_role: selectedUser.value.role,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({ severity: 'success', summary: 'Success', detail: 'Role updated successfully', life: 3000 });
        showEditModal.value = false;
        fetchUsers();
        editSubmitted.value = false;
      },
      onError: () => {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to update role', life: 3000 });
      },
    });
  };

  const resetUserPassword = () => {
    resetSubmitted.value = true;
    if (!selectedUser.value || !newPassword.value) return;

    router.post('/admin/users/password', {
      user_id: selectedUser.value.id,
      new_password: newPassword.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({ severity: 'success', summary: 'Success', detail: 'Password reset successfully', life: 3000 });
        showResetModal.value = false;
        newPassword.value = '';
        resetSubmitted.value = false;
      },
      onError: () => {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to reset password', life: 3000 });
      },
    });
  };

  const fetchUserHistory = async () => {
    if (!selectedUser.value) return;
    isLoadingHistory.value = true;
    historyError.value = null;
    try {
      const response = await fetch('/admin/api-logs', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ username: selectedUser.value.name }),
      });
      const json = await response.json();
      userHistory.value = json.data ?? [];
    } catch (error) {
      historyError.value = 'Failed to fetch user history';
      toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to fetch user history', life: 3000 });
    } finally {
      isLoadingHistory.value = false;
    }
  };

  const viewUserHistory = async (user) => {
    selectedUser.value = user;
    await fetchUserHistory();
    showHistoryModal.value = true;
  };

  return {
    users, showModal, submitted, menu, newUser, roles,
    fetchUsers, registerUser, closeModal, resetForm,
    showEditModal, selectedUser, editSubmitted, updateUserRole,
    showResetModal, newPassword, resetSubmitted, resetUserPassword,
    showHistoryModal, userHistory, isLoadingHistory, historyError,
    viewUserHistory, fetchUserHistory,
  };
}
