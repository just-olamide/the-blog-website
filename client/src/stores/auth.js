import { defineStore } from 'pinia'
import axios from '@/plugins/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    isAuthenticated: false,
  }),

  getters: {
    getUser: (state) => state.user,
    getToken: (state) => state.token,
    isLoggedIn: (state) => state.isAuthenticated,
  },

  actions: {
    async login(credentials) {
      try {
        const response = await axios.post('/login', credentials)
        const { user, token } = response.data

        this.user = user
        this.token = token
        this.isAuthenticated = true

        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        return Promise.resolve(response)
      } catch (error) {
        return Promise.reject(error)
      }
    },

    async register(userData) {
      try {
        const response = await axios.post('/register', userData)
        const { user, token } = response.data

        this.user = user
        this.token = token
        this.isAuthenticated = true

        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        return Promise.resolve(response)
      } catch (error) {
        return Promise.reject(error)
      }
    },

    async logout() {
      try {
        await axios.post('/logout')

        this.user = null
        this.token = null
        this.isAuthenticated = false

        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']

        return Promise.resolve()
      } catch (error) {
        return Promise.reject(error)
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/user')
        this.user = response.data
        this.isAuthenticated = true
        return Promise.resolve(response)
      } catch (error) {
        return Promise.reject(error)
      }
    },
  },
})
