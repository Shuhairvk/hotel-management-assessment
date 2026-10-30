import axios from 'axios';

const api = axios.create({ baseURL: '/api' });

api.interceptors.request.use((c) => {
    const t = localStorage.getItem('token');
    if (t) c.headers.Authorization = 'Bearer ' + t;
    c.headers.Accept = 'application/json';
    return c;
});

api.interceptors.response.use((r) => r, (e) => {
    if (e.response?.status === 401 && !location.pathname.startsWith('/login')) {
        localStorage.clear();
        location.href = '/login';
    }
    return Promise.reject(e);
});

export const user = () => JSON.parse(localStorage.getItem('user') || 'null');
export const can = (m) => {
    const u = user();
    return !!u && (u.role === 'admin' || (u.permissions || []).includes(m));
};
export const errMsg = (e) =>
    Object.values(e.response?.data?.errors || {})[0]?.[0] || e.response?.data?.message || 'Something went wrong';

export default api;
