import * as React from 'react';
import {
  Box, Button, ButtonProps, Checkbox, TextField, Theme,
} from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import { useForm } from 'react-hook-form';
import categoryHttp from '../../util/http/category-http';

const useStyles = makeStyles((theme: Theme) => ({
  submit: {
    margin: theme.spacing(1),
  },
}));

const Form = () => {
  const classes = useStyles();
  const buttonProps: ButtonProps = {
    className: classes.submit,
    color: 'secondary',
    variant: 'contained',
  };

  const { register, handleSubmit, getValues } = useForm({
    defaultValues: {
      is_active: true,
    },
  });

  async function onSubmit(formData, event) {
    await categoryHttp.create(formData);
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <TextField
        name="name"
        label="Nome"
        fullWidth
        variant="outlined"
        margin="normal"
        inputRef={register}
      />

      <TextField
        name="description"
        label="Descrição"
        multiline
        rows="4"
        fullWidth
        variant="outlined"
        margin="normal"
        inputRef={register}
      />

      <Checkbox
        name="is_active"
        color="primary"
        inputRef={register}
        defaultChecked
      />
      Ativo?

      <Box dir="rtl">
        <Button {...buttonProps} color="primary" onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
        <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
      </Box>
    </form>
  );
};

export default Form;
