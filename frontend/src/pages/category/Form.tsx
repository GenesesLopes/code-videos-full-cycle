import * as React from 'react';
import { Box, Button, ButtonProps, Checkbox, makeStyles, TextField, Theme } from '@material-ui/core';
import { useForm, Controller } from 'react-hook-form';
import categoryHttp from '../../utils/http/category-http';
import { CategoryResponse } from './types';
import { useHistory } from 'react-router';

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

const Form = () => {

    const history = useHistory();

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        variant: 'contained',
        className: classes.submit
    }

    const defaultValues = {
        name: '',
        description: '',
        is_active: true
    }

    const { getValues, control, handleSubmit } = useForm({
        defaultValues
    })

    const OnSubmit = async (formData, event) => {
        let eventType = event.type
        try {
            let { data } = await categoryHttp.create<CategoryResponse>(formData)
            console.log(data)
            if (eventType === 'click') {
                // console.log('redirecionar para listagem')
                history.push('/categories')
            }
        } catch (error) {
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
                name='description'
                control={control}
                render={
                    ({ field }) => <TextField
                        {...field}
                        label='Descrição'
                        multiline
                        rows='4'
                        fullWidth
                        variant={'outlined'}
                        margin={'normal'}
                    />
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
                <Button
                    color={'primary'}

                    {...buttonProps}
                    onClick={(event) => OnSubmit(getValues(), event)}
                >
                    Salvar
                </Button>
                <Button
                    {...buttonProps}
                    type='submit'
                >
                    Salvar e continuar editando
                </Button>
            </Box>
        </form>
    );
};

export default Form;