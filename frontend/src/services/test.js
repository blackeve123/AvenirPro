import api from './api';

export const getQuestions = (page = 1) => api.get(`/questions?page=${page}`);
export const submitAnswer = (data) => api.post('/test/answer', data);
export const calculateTestResults = () => api.post('/test/calculate');
export const getUserResults = () => api.get('/my-results');
export const getTestStatus = () => api.get('/test/status');
export const resetTest = () => api.delete('/test/reset');


// Admin test functions
export const createQuestion = (questionData) => api.post('/admin/questions', questionData);
export const updateQuestion = (id, questionData) => api.put(`/admin/questions/${id}`, questionData);
export const deleteQuestion = (id) => api.delete(`/admin/questions/${id}`);
