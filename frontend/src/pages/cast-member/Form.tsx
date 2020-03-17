import * as React from 'react';
import {
  Box, Button, ButtonProps, FormControl, FormControlLabel, FormLabel,
  Radio, RadioGroup, TextField, Theme,
} from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import { useForm } from 'react-hook-form';
import { useEffect } from 'react';
import castMemberHttp from '../../util/http/cast-meber-http';

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

  const {
    register, handleSubmit, getValues, setValue,
  } = useForm({
    defaultValues: {
      is_active: true,
    },
  });

  useEffect(() => {
    register({ name: 'type' });
  }, [register]);

  async function onSubmit(formData, event) {
    await castMemberHttp.create(formData);
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

      <FormControl margin="normal">
        <FormLabel component="legend">Tipo</FormLabel>
        <RadioGroup
          name="type"
          onChange={(e) => {
            // eslint-disable-next-line radix
            setValue('type', parseInt(e.target.value));
          }}
        >
          <FormControlLabel value="1" control={<Radio color="primary" />} label="Diretor" />
          <FormControlLabel value="2" control={<Radio color="primary" />} label="Ator" />
        </RadioGroup>
      </FormControl>

      <Box dir="rtl">
        <Button {...buttonProps} color="primary" onClick={() => onSubmit(getValues(), null)}>
          Salvar
        </Button>
        <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
      </Box>
    </form>
  );
};

export default Form;
