import React, { useEffect, useState } from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import {format, parseISO} from 'date-fns'
import categoryHttp from '../../utils/http/category-http';
import { Category, CategoryResponse } from './types';
import { BadgeNo, BadgeYes } from '../../components/Badge/index';


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
                return value ? <BadgeYes /> : <BadgeNo />;
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

    const [data, setData] = useState<Category[]>([]);

    useEffect(() => {
        categoryHttp
            .list<CategoryResponse>()
            .then(({data}) => setData(data.data))
            .catch(error => console.error(error))
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