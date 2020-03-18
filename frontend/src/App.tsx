import React from 'react';
import {Box, CssBaseline, MuiThemeProvider} from '@material-ui/core';
import { BrowserRouter } from 'react-router-dom';
import { Navbar } from './components/Navbar';
import AppRouter from './routes/AppRouter';
import Breadcrumbs from './components/Breadcrumps';
import theme from './theme';

const App = () => (
  <>
    <MuiThemeProvider theme={theme}>
      <CssBaseline />
      <BrowserRouter>
        <Navbar />
        <Box paddingTop="70px">
          <Breadcrumbs />
          <AppRouter />
        </Box>
      </BrowserRouter>
    </MuiThemeProvider>
  </>
);

export default App;
