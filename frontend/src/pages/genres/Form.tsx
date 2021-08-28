import React, {useEffect, useState} from 'react';
import { Box, Button, ButtonProps, Checkbox, makeStyles, MenuItem, TextField, Theme } from '@material-ui/core';
import { useForm, Controller } from 'react-hook-form';
import genreHttp from '../../utils/http/genre-http';
import { GenresResponse } from './types';
import categoryHttp from '../../utils/http/category-http';
import { Category, CategoryResponse } from '../category/types';
import { useHistory } from 'react-router';

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

const Form = () => {

    const [dataCategories, setDataCategories] = useState<Category[]>([]);

    useEffect(() => {
        categoryHttp
            .list<CategoryResponse>()
            .then(({data}) => {
                setDataCategories(data.data)
            })
            .catch(error => console.error(error))
    },[])

    const history = useHistory();

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        variant: 'contained',
        className: classes.submit,
        color: 'secondary'
    }

    const defaultValues = {
        name: '',
        categories_id: [],
        is_active: true
    }

    const { getValues, control, handleSubmit } = useForm({
        defaultValues
    })
    
    const OnSubmit = async (formData, event) => {
        let eventType = event.type
        try{
            let { data } = await genreHttp.create<GenresResponse>(formData)
            console.log(data.data)
            if(eventType === 'click'){
                console.log('redirecionar para listagem')
                history.push('/genres')
            }
        }catch(error){
            console.error(error)
        }
        
    }

    return (
        <form onSubmit={handleSubmit(OnSubmit)}>
            <Controller
                name='name'
                control={control}
                render={
                    ({ field }) => <TextField
                        {...field}
                        label='Nome'
                        fullWidth
                        variant={'outlined'}
                    />
                }
            />
            <Controller
                name='categories_id'
                control={control}
                render={
                    ({ field }) => (
                        <TextField
                            { ...field }
                            // onChange={(element) => {
                            //     console.log(element.target.value)
                            // }}
                            select
                            label='Categorias'
                            fullWidth
                            SelectProps={{
                                multiple: true
                            }}
                            variant={'outlined'}
                            margin={'normal'}
                        >
                            <MenuItem value='' disabled selected>
                                <em>Selecione Categorias</em>
                            </MenuItem>
                            {
                                dataCategories.map(categories => (
                                    <MenuItem key={categories.id} value={categories.id}>
                                        {categories.name}
                                    </MenuItem>
                                    )
                                )
                             }
                        </TextField>
                    )
                }

            />
            <Controller 
                name='is_active'
                control={control}
                render={
                    ({ field }) => <Checkbox {...field} defaultChecked={defaultValues.is_active} />
                }
            />
            Ativo ?
            <Box dir='rtl'>
                <Button {...buttonProps} onClick={(event) => OnSubmit(getValues(),event)}>Salvar</Button>
                <Button {...buttonProps} type='submit'>Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};

export default Form;