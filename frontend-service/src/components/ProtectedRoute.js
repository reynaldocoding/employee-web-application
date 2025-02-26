import React from 'react';
import { Navigate } from 'react-router-dom';
import { setAuthToken } from '../services/api';

const ProtectedRoute = ({ children }) => {
  const token = localStorage.getItem('token');
  if (token) {
    setAuthToken(token);
    return children;
  }
  return <Navigate to="/login" />;
};

export default ProtectedRoute;
