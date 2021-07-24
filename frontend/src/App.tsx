import { Box, MuiThemeProvider } from '@material-ui/core';
import React from 'react';
import { BrowserRouter } from 'react-router-dom';
import Breadcrumbs from './components/Breadcrumps/index';
import { Navbar } from './components/Navbar';
import AppRouter from './routes/AppRouter';
import theme from './themes';
const App: React.FC = () => {
  return (
    <>
      <MuiThemeProvider theme={theme}>
        <BrowserRouter>
          <Navbar />
          <Box paddingTop={'70px'}>
            <Breadcrumbs />
            <AppRouter />
          </Box>
        </BrowserRouter>
      </MuiThemeProvider>
    </>
  )
}

export default App;
