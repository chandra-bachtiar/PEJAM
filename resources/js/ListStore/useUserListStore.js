import axios from '@axios'
import { defineStore } from 'pinia'

export const useUserListStore = defineStore('UserListStore', {
  actions: {
    // 👉 Fetch users data
    fetchUsers(params) { return axios.get('/api/user', { params }) },

    // 👉 Add User
    addUser(userData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/user',
          method: 'POST',
          data: userData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })

          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchUser(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/user/${id}`).then(response => resolve(response.data.user)).catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateUser(userData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/user/${userData.get('id')}`,
          method: 'POST',
          data: userData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete User
    deleteUser(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/user/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // import user
    importUser(userData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/user/import',
          method: 'POST',
          data: userData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
})
