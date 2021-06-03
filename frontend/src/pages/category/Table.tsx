import React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';

const colunsDefinitions: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome'
    },
    {
        name: 'is_active',
        label: 'Ativo?'
    },
    {
        name: 'created_at',
        label: 'Criado em'
    }
];

const data = [
    {
        name: 'Teste',
        is_active: true,
        created_at: '2019-12-12'
    },
    {
        name: 'Teste2',
        is_active: false,
        created_at: '2020-12-12'
    }
];

type Props = {};
const Table = (props: Props) => {
    return (
        <MUIDataTable 
            title='Listagem de Categorias'
            columns={colunsDefinitions}
            data={data}
        />
    );
};

export default Table;