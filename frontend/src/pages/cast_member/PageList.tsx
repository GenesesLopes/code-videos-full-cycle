import React from 'react';
import { Box, Fab } from '@material-ui/core';
import { Link } from 'react-router-dom';
import Page from '../../components/Pages'
import AddIcon from '@material-ui/icons/Add'
import Table from './Table';


const PageList = () => {
    return (
        <Page title="Listagem de Membros">
            <Box dir={'rtl'}>
                <Fab 
                    title="Adicionar Membros"
                    size="small"
                    component={Link}
                    to="/cast-members/create"
                >
                <AddIcon />
                </Fab>
            </Box>
            <Box>
                <Table />
            </Box>
        </Page>
    );
};

export default PageList;