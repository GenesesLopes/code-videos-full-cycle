import React, { useEffect, useState } from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { httpVideo } from '../../utils/http';
import {format, parseISO} from 'date-fns'


const TypeMembersMap = {
    1: 'Diretor',
    2: 'Ator'
};

const colunsDefinitions: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome'
    },
    {
        name: 'type',
        label: 'Tipo',
        options: {
            customBodyRender(value){
                return TypeMembersMap[value];
            }
        }
    },
    {
        name: 'created_at',
        label: 'Criado em',
        options: {
            customBodyRender(value){
                return <span>{format(parseISO(value),'dd/MM/yyyy')}</span>
            }
        }
    }
];

type Props = {};
const Table = (props: Props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        httpVideo.get('cast_members').then(response =>{
            setData(response.data.data)
        }).catch(error => {
            console.error(error)
        })
    },[])
    
    return (
        <MUIDataTable 
            title='Listagem de Membros'
            columns={colunsDefinitions}
            data={data}
        />
    );
};

export default Table;