import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useUserActions(setShowEditModal, setSelectedUser, showResetModal, setShowHistoryModal) {
  const menuItems = ref([
    {
      label: 'View History',
      icon: 'pi pi-history',
      command: (event) => viewHistory(event.item.instance),
    },
    {
      label: 'Edit Role',
      icon: 'pi pi-user-edit',
      command: (event) => editRole(event.item.instance),
    },
    {
      label: 'Reset Password',
      icon: 'pi pi-key',
      command: (event) => {
        setSelectedUser(event.item.instance);
        showResetModal(true);
      },
    },
    {
      label: 'Delete',
      icon: 'pi pi-trash',
      command: (event) => deleteUser(event.item.instance),
    },
  ]);

  const viewHistory = (user) => {
    setSelectedUser(user);
    setShowHistoryModal(true);
  };

  const editRole = (user) => {
    setSelectedUser(user);
    setShowEditModal(true);
  };

  const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
      router.delete(`/admin/users/${user.id}`, {
        preserveScroll: true,
      });
    }
  };

  const toggleMenu = (event, user, menuRef) => {
    menuRef.toggle(event);
    menuItems.value.forEach(item => item.instance = user);
  };

  return { menuItems, toggleMenu, viewHistory, editRole, deleteUser };
}
