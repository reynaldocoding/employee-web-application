import React, { useState } from 'react';
import { api, setAuthToken } from '../services/api';
import { useNavigate, Link } from 'react-router-dom';

const Login = () => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await api.post('/login_check', { username, password });
      const { token } = response.data;
      setAuthToken(token);
      localStorage.setItem('token', token);
      localStorage.setItem('username', username);
      navigate('/employees');
    } catch (error) {
      console.error('Error de inicio de sesion', error);
      setError(`Error de inicio de sesion ${error?.response?.data?.message}`);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h2>Login</h2>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      <input
        type="username"
        value={username}
        onChange={(e) => setUsername(e.target.value)}
        placeholder="email"
        required
      />
      <input
        type="password"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        placeholder="Password"
        required
      />
      <button type="submit">Iniciar sesión</button>
      <p>
        Registrate como usuario <Link to="/register">Ir al Formulario de Registro</Link>
      </p>
    </form>
  );
};

export default Login;
