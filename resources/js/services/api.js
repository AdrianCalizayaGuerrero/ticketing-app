import axios from 'axios'

const api = axios.create({
    baseURL: '/api',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
})

api.interceptors.request.use(config => {
    const token = localStorage.getItem('token')
    if (token) config.headers.Authorization = `Bearer ${token}`
    return config
})

api.interceptors.response.use(
    res => res,
    err => {
        if (err.response?.status === 401) {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            window.location.href = '/login'
        }
        return Promise.reject(err)
    }
)

export default {
    login(data)            { return api.post('/login', data) },
    logout()               { return api.post('/logout') },
    me()                   { return api.get('/me') },
    getTickets(page = 1)   { return api.get(`/tickeds?page=${page}`) },
    getTicket(id)          { return api.get(`/tickeds/${id}`) },
    createTicket(data)     { return api.post('/tickeds', data) },
    updateTicket(id, data) { return api.put(`/tickeds/${id}`, data) },
    deleteTicket(id)       { return api.delete(`/tickeds/${id}`) },
    getAgents()            { return api.get('/agents?per_page=100') },
    getUsers()             { return api.get('/users') },
    getCategories()        { return api.get('/categories') },
    getPriorities()        { return api.get('/priorities') },
    sendMessage(data)      { return api.post('/messages', data) },
    getStatusHistories()   { return api.get('/status_histories') },
    createUser(data)       { return api.post('/users', data) },
    deleteUser(id)         { return api.delete(`/users/${id}`) },
}
