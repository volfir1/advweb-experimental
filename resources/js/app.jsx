import React, { useEffect, useState } from 'react';
import ReactDOM from 'react-dom/client';
import Header from './components/header';
import Sidebar from './components/sidebar';
import '../css/app.css';
import axios from 'axios';

function App() {
  const [user, setUser] = useState(null);

  useEffect(() => {
    const fetchUserProfile = async () => {
      try {
        const response = await axios.get('/api/user/profile'); // Ensure this URL matches the route you defined
        console.log(response.data);  // Log user data for debugging
        setUser(response.data);
      } catch (error) {
        console.error('Error fetching user profile:', error);
      }
    };

    fetchUserProfile(); // Call the function when the component mounts
  }, []);

  if (!user) {
    return <div>Loading...</div>;
  }

  const role = user.is_admin ? 'admin' : 'customer';

  return (
    <div className="App">
      <Header user={{ ...user, role }} />
      <div className="main-wrapper">
        {role === 'admin' && <Sidebar />}
        <div className="content">
          <div className="welcome-message">
            {role === 'admin' ? (
              <div>
                
              </div>
            ) : (
              <div>
               
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('hello-react');
  if (container) {
    const root = ReactDOM.createRoot(container);
    root.render(<App />);
  } else {
    console.error('Element with id "hello-react" not found.');
  }
});
