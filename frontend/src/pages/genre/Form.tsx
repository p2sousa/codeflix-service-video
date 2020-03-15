import * as React from 'react';
import {
  Box, Button, ButtonProps, MenuItem, TextField, Theme,
} from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import { useForm } from 'react-hook-form';
import { useEffect, useState } from 'react';
import genreHttp from '../../util/http/genre-http';
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
    variant: 'outlined',
  };

  const [categories, setCategories] = useState<any[]>([]);
  const {
    register, handleSubmit, getValues, setValue, watch,
  } = useForm({
    defaultValues: {
      categories_id: [],
    },
  });

  useEffect(() => {
    register({ name: 'categories_id' });
  }, [register]);

  useEffect(() => {
    async function getCategories() {
      const response = await categoryHttp.list();
      setCategories(response.data.data);
    }
    getCategories();
  }, []);

  async function onSubmit(formData, event) {
    const response = await genreHttp.create(formData);
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
        select
        name="categories_id"
        value={watch('categories_id')}
        label="Categorias"
        fullWidth
        variant="outlined"
        margin="normal"
        onChange={(e) => {
          setValue('categories_id', e.target.value);
        }}
        SelectProps={{
          multiple: true,
        }}
      >
        <MenuItem value="" disabled>
          <em>Selecione categorias</em>
        </MenuItem>
        {
          categories.map(
            (category, key) => (
              <MenuItem key={key} value={category.id}>{category.name}</MenuItem>
            ),
          )
        }
      </TextField>

      <Box dir="rtl">
        <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
        <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
      </Box>
    </form>
  );
};

export default Form;
