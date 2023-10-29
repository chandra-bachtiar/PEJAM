import axios from '@axios'
import { defineStore } from 'pinia'

export const useVoteListStore = defineStore('VoteListStore', {
  actions: {
    // 👉 Fetch users data
    fetchVotes(params) { return axios.get('/api/vote', { params }) },

    // 👉 check is already voted
    checkIsAlreadyVoted(params) { return axios.get('/api/vote/check', { params }) },

    // 👉 Add Vote
    addVote(voteData) {
      return new Promise((resolve, reject) => {
        axios({
          url: '/api/vote',
          method: 'POST',
          data: voteData,
          headers: { 'Content-Type': 'application/json' },
        })

          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchVote(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/vote/${id}`).then(response => resolve(response.data.vote)).catch(error => reject(error))
      })
    },

    // 👉 Update Vote
    updateVote(voteData) {
      return new Promise((resolve, reject) => {
        axios({
          url: `/api/vote/${voteData.get('id')}`,
          method: 'POST',
          data: voteData,
          headers: { 'Content-Type': 'multipart/form-data' },
        })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete Vote
    deleteVote(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/api/vote/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
