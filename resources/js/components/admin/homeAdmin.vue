<template>
  <div class="admin-page">
    <h1>Admin Dashboard</h1>
    <button @click="showModal = true" class="dashboard-button">Register User</button>

    <!-- Registration Modal -->
    <Modal v-if="showModal" @close="closeModal">
      <h2>Register User</h2>
      <form @submit.prevent="registerUser">
        <div>
          <label for="email">Email:</label>
          <input type="email" id="email" v-model="newUser.email" required />
        </div>
        <div>
          <label for="name">Name:</label>
          <input type="text" id="name" v-model="newUser.name" required />
        </div>
        <div>
          <label for="role">Role:</label>
          <input type="text" id="role" v-model="newUser.role" required />
        </div>
        <div>
          <button type="submit">Submit</button>
          <button type="button" @click="closeModal">Cancel</button>
        </div>
      </form>
    </Modal>

    <table class="user-table">
      <thead>
        <tr>
          <th>Email</th>
          <th>Name</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.email }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.role }}</td>
          <td>
            <div class="actions-dropdown">
              <button @click="toggleDropdown(user.id)">Edit</button>
              <div v-if="dropdownVisible[user.id]" class="dropdown-menu">
                <button @click="viewHistory(user)">View History</button>
                <button @click="editRole(user)">Edit Role</button>
                <button @click="deleteUser(user)">Delete User</button>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
// import Modal from Modal; // Import the Modal component
import Modal from '../sheets/modal.vue';

export default {
  components: { Modal },
  setup() {
    const users = ref([]);
    const dropdownVisible = reactive({});
    const showModal = ref(false);
    const newUser = reactive({
      email: '',
      name: '',
      role: '',
    });

    const fetchUsers = async () => {
      try {
        const response = await fetch('/api/admin/listuser');
        const data = await response.json();
        users.value = data;
      } catch (error) {
        console.error('Failed to fetch users:', error);
      }
    };

    const toggleDropdown = (id) => {
      dropdownVisible[id] = !dropdownVisible[id];
    };

    const viewHistory = (user) => {
      alert(`Viewing history for: ${user.name}`);
    };

    const editRole = (user) => {
      alert(`Editing role for: ${user.name}`);
    };

    const deleteUser = (user) => {
      alert(`Deleting user: ${user.name}`);
      users.value = users.value.filter(u => u.id !== user.id);
    };

    const registerUser = () => {
      users.value.push({ ...newUser, id: Date.now() });
      closeModal();
    };

    const closeModal = () => {
      showModal.value = false;
      newUser.email = '';
      newUser.name = '';
      newUser.role = '';
    };

    onMounted(() => {
      fetchUsers();
    });

    return {
      users,
      dropdownVisible,
      showModal,
      newUser,
      toggleDropdown,
      viewHistory,
      editRole,
      deleteUser,
      registerUser,
      closeModal,
    };
  },
};
</script>

<style scoped>
.admin-page {
  padding: 20px;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.user-table th,
.user-table td {
  border: 1px solid #ccc;
  padding: 10px;
  text-align: left;
}

.user-table th {
  background-color: #f4f4f4;
}

.actions-dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-menu {
  display: flex;
  flex-direction: column;
  position: absolute;
  top: 100%;
  left: 0;
  background-color: white;
  border: 1px solid #ccc;
  padding: 5px 0;
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
  z-index: 1;
}

.dropdown-menu button {
  background: none;
  border: none;
  padding: 10px;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.dropdown-menu button:hover {
  background-color: #f0f0f0;
}

.user-table button {
  padding: 5px 10px;
  cursor: pointer;
}

.user-table button:last-child {
  margin-right: 0;
}

.dashboard-button {
  width: auto;
  margin-bottom: 20px;
  padding: 5px 10px;
  cursor: pointer;
}
</style>
