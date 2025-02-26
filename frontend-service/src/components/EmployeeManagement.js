import React, { useEffect, useState } from 'react';
import { api } from '../services/api';
import { useNavigate } from 'react-router-dom';

const EmployeeManagement = () => {
  const [employees, setEmployees] = useState([]);
  const [name, setName] = useState('');
  const [lastNames, setLastNames] = useState('');
  const [position, setPosition] = useState('');
  const [birthdate, setBirthdate] = useState('');
  const [email, setEmail] = useState('');
  const [positions, setPositions] = useState([]);
  const [selectedEmployee, setSelectedEmployee] = useState(null);
  const [username, setUsername] = useState('');
  const [searchName, setSearchName] = useState('');
  const [error, setError] = useState('');
  const navigate = useNavigate();

  useEffect(() => {
    const fetchEmployees = async () => {
      try {
        let url = '/employees';
        if (searchName) {
          url += `?name=${searchName}`;
        }
        const response = await api.get(url);
        setEmployees(response.data.data);
      } catch (error) {
        console.error('Fallo al obtener los empleados', error);
      }
    };

    const fetchPositions = async () => {
      try {
        const response = await api.get('/positions');
        setPositions(response.data.data.positions);
      } catch (error) {
        console.error('Fallo al obtener los puestos de trabajo', error);
      }
    };

    const fetchUsername = () => {
      const storedUsername = localStorage.getItem('username');
      if (storedUsername) {
        setUsername(storedUsername);
      }
    };

    fetchEmployees();
    fetchPositions();
    fetchUsername();
  }, [searchName]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const employeeData = { name, lastNames, position, birthdate, email };
      if (selectedEmployee) {
        await api.put(`/employees/${selectedEmployee.id}`, employeeData);
        setSelectedEmployee(null);
      } else {
        await api.post('/employees', employeeData);
      }
      setName('');
      setLastNames('');
      setPosition('');
      setBirthdate('');
      setEmail('');
      setError('');
      fetchEmployees();
    } catch (error) {
      console.error('No se pudo registrar al empleado', error);
      setError(`No se pudo registrar al empleado ${error?.response?.data?.message}`);
    }
  };

  const handleEdit = (employee) => {
    setSelectedEmployee(employee);
    setName(employee.name);
    setLastNames(employee.lastNames);
    setPosition(employee.position);
    setBirthdate(employee.birthdate);
    setEmail(employee.email);
  };

  const handleDelete = async (id) => {
    try {
      await api.delete(`/employees/${id}`);
      fetchEmployees();
    } catch (error) {
      console.error('No se pudo dar de baja al empleado', error);
    }
  };

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('username');
    navigate('/login');
  };

  const fetchEmployees = async () => {
    try {
      let url = '/employees';
      if (searchName) {
        url += `?name=${searchName}`;
      }
      const response = await api.get(url);
      setEmployees(response.data.data);
    } catch (error) {
      console.error('Fallo al obtener los empleados', error);
    }
  };

  return (
    <div>
      <hr />
      <div style={{ display: 'flex', alignItems: 'center' }}>
        <h3>Bienvenido, {username}!</h3>
        <button onClick={handleLogout}>Cerrar sesión</button>
      </div>
      <hr />
      <h1>Gestión de Empleados</h1>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      <h2>Formulario</h2>
      <form onSubmit={handleSubmit}>
        <input
          type="text"
          value={name}
          onChange={(e) => setName(e.target.value)}
          placeholder="Nombre"
          required
        />
        <input
          type="text"
          value={lastNames}
          onChange={(e) => setLastNames(e.target.value)}
          placeholder="Apellidos"
          required
        />
        <select value={position} onChange={(e) => setPosition(e.target.value)} required>
          <option value="">Seleccione el Puesto de Trabajo</option>
          {positions && positions.length > 0 ? (
            positions.map((pos) => (
              <option key={pos} value={pos}>
                {pos}
              </option>
            ))
          ) : (
            <option disabled>No hay puestos de trabajo disponibles</option>
          )}
        </select>
        <input
          type="date"
          value={birthdate}
          onChange={(e) => setBirthdate(e.target.value)}
          placeholder="Fecha de nacimiento"
          required
        />
        <input
          type="email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          placeholder="Correo Electrónico"
          required
        />
        <button type="submit">{selectedEmployee ? 'Modificar' : 'Contratar'} Empleado</button>
      </form>
      <br />
      <div>
        <h2>Búsqueda de empleados por Nombre</h2>
        <input
          type="text"
          value={searchName}
          onChange={(e) => setSearchName(e.target.value)}
          placeholder="Nombre del empleado"
          style={{ marginBottom: '10px' }}
        />
      </div>
      <table border="1">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Puesto de Trabajo</th>
            <th>Fecha de nacimiento</th>
            <th>Correo Electrónico</th>
            <th>Opciones</th>
          </tr>
        </thead>
        <tbody>
          {employees && employees.length > 0 ? (
            employees.map((employee) => (
              <tr key={employee.id}>
                <td>{employee.name}</td>
                <td>{employee.lastNames}</td>
                <td>{employee.position}</td>
                <td>{new Date(employee.birthdate).toLocaleDateString()}</td>
                <td>{employee.email}</td>
                <td>
                  <button onClick={() => handleEdit(employee)}>Modificar</button>
                  <button onClick={() => handleDelete(employee.id)}>Dar de baja</button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="6">No existen empleados registrados</td>
            </tr>
          )}
        </tbody>
      </table>
      <br />
    </div>
  );
};

export default EmployeeManagement;
