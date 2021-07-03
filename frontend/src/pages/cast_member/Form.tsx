import * as React from 'react';
import { Box, Button, ButtonProps, Radio, FormControl, FormControlLabel, RadioGroup, makeStyles, TextField, Theme, FormLabel } from '@material-ui/core';
import { useForm, Controller } from 'react-hook-form';
// import categoryHttp from '../../utils/http/category-http';
// import { CategoryResponse } from './types';
// import { useHistory } from 'react-router';

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

const Form = () => {

    // const history = useHistory();

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        variant: 'outlined',
        className: classes.submit
    }

    const defaultValues = {
        name: '',
        type: '1'
    }

    const { getValues, control, handleSubmit } = useForm({
        defaultValues
    })
    
    const OnSubmit = async (formData, event) => {
        let eventType = event.type
        console.log(
            eventType,
            formData
        )
        // try{
        //     let { data } = await categoryHttp.create<CategoryResponse>(formData)
        //     console.log(data)
        //     if(eventType === 'click'){
        //         // console.log('redirecionar para listagem')
        //         history.push('/categories')
        //     }
        // }catch(error){
        //     console.error(error)
        // }
        
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
                name='type'
                control={control}
                render={
                    ({ field }) => (
                        <FormControl component='fieldset' margin="normal">
                            <FormLabel component='legend'>Tipo</FormLabel>
                            <RadioGroup {...field}>
                                <FormControlLabel value='1' control={ <Radio/> } label='Diretor' />
                                <FormControlLabel value='2' control={ <Radio/> } label='Ator' />
                            </RadioGroup>
                        </FormControl>
                    )
                }

            />

            <Box dir='rtl'>
                <Button {...buttonProps} onClick={(event) => OnSubmit(getValues(),event)}>Salvar</Button>
                <Button {...buttonProps} type='submit'>Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};

export default Form;