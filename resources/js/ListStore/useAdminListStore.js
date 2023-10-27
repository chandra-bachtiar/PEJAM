import axios from '@axios'
import { defineStore } from 'pinia'

export const useAdminListStore = defineStore('AdminListStore', {
  actions: {
    // 👉 Fetch admins data
    fetchAdmins(params) { return axios.get('/api/account/admin', { params }) },

    // 👉 Add Admin
    addAdmin(adminData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/account/admin',
          method: 'POST',
          data: adminData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })

          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single admin
    fetchAdmin(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/account/admin/${id}`).then(response => resolve(response.data.admin)).catch(error => reject(error))
      })
    },

    // 👉 Update Admin
    updateAdmin(adminData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/account/admin/${adminData.get('id')}`,
          method: 'POST',
          data: adminData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete Admin
    deleteAdmin(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/account/admin/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
