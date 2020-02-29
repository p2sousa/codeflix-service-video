import React from 'react';
import { Box } from '@material-ui/core';
import { BrowserRouter } from 'react-router-dom';
import { Navbar } from './components/Navbar';
import AppRouter from './routes/AppRouter';
import Breadcrumbs from "./components/Breadcrumps";

const App = () => (
  <>
    <BrowserRouter>
      <Navbar />
      <Box paddingTop="70px">
        <Breadcrumbs/>
        <AppRouter />
      </Box>
    </BrowserRouter>
  </>
);

export default App;
