import React, { useEffect, useState } from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { httpVideo } from '../../utils/http';
import { Chip } from '@material-ui/core';
import {format, parseISO} from 'date-fns'

const colunsDefinitions: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome'
    },
    {
        name: 'categories',
        label: 'Categorias',
        options: {
            customBodyRender(value){
                return value.map(data => data.name).join(', ');
            }
        }
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
        httpVideo.get('genre').then(response =>{
            setData(response.data.data)
        }).catch(error => {
            console.error(error)
        })
    },[])
    
    return (
        <MUIDataTable 
            title='Listagem de Gêneros'
            columns={colunsDefinitions}
            data={data}
        />
    );
};

export default Table;