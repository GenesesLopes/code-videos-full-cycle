import { Box } from '@material-ui/core';
import React from 'react';
import { Navbar } from './components/Navbar';
import Page from './components/Pages';
const App: React.FC = () => {
  return (
    <>
      <Navbar/>
      <Box paddingTop={'70px'}>
        <Page title={'categorias'}>
          Conteúdo
        </Page>
      </Box>
    </>
  )
}

export default App;
