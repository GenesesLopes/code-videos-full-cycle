import * as React from 'react';
import { Box, Button, ButtonProps, Checkbox, makeStyles, TextField, Theme } from '@material-ui/core';
import { useForm, Controller } from 'react-hook-form';

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

const Form = () => {

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        variant: 'outlined',
        className: classes.submit
    }

    const { getValues, control } = useForm()

    return (
        <form>
            <Controller
                name='name'
                control={control}
                defaultValue=""
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
                defaultValue=""
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
                defaultValue={false}
                render={
                    ({ field }) => <Checkbox {...field} />
                }
            />
            Ativo ?
            <Box dir='rtl'>
                <Button {...buttonProps} onClick={() => console.log(getValues())}>Salvar</Button>
                <Button {...buttonProps} type='submit'>Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};

export default Form;