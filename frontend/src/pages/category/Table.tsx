import React, { useEffect, useState } from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { Chip } from '@material-ui/core';
import {format, parseISO} from 'date-fns'
import categoryHttp from '../../utils/http/category-http';

const colunsDefinitions: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome'
    },
    {
        name: 'is_active',
        label: 'Ativo?',
        options: {
            customBodyRender(value){
                return value ? <Chip label='Sim' color='primary' /> : <Chip label='Não' color='secondary'/>
            }
        }
    },
    {
        name: 'created_at',
        label: 'Criado em',
        options: {
            customBodyRender(value){
                return <span>{format(parseISO(value),'dd/mm/yyy')}</span>
            }
        }
    }
];
type Props = {};
const Table = (props: Props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        categoryHttp
            .list()
            .then(({data}) => setData(data.data))
            .catch(error => console.error(error))
        // httpVideo.get('categories').then(response =>{
        //     setData(response.data.data)
        // }).catch(error => {
        //     console.error(error)
        // })
    },[])
    
    return (
        <MUIDataTable 
            title='Listagem de Categorias'
            columns={colunsDefinitions}
            data={data}
        />
    );
};

export default Table;