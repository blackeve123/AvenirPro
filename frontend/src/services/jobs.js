import api from './api';

export const getJobs = (params) => api.get('/jobs', { params });
export const getCategories = () => api.get('/categories');
export const getJobById = (id) => api.get(`/jobs/${id}`);

// Admin functions
export const createJob = (jobData) => api.post('/admin/jobs', jobData, {
    headers: { 'Content-Type': 'multipart/form-data' }
});

export const updateJob = (id, jobData) => {
    // We use POST and spoof PUT to handle form-data with files in Laravel
    jobData.append('_method', 'PUT');
    return api.post(`/admin/jobs/${id}`, jobData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    });
};

export const deleteJob = (id) => api.delete(`/admin/jobs/${id}`);
export const addJobStep = (jobId, stepData) => api.post(`/admin/jobs/${jobId}/steps`, stepData);
export const deleteJobStep = (stepId) => api.delete(`/admin/steps/${stepId}`);

