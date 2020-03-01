import * as React from 'react';
import MUIDataTable, { MUIDataTableColumnDef } from 'mui-datatables';
import { useEffect, useState } from 'react';
import { Chip } from '@material-ui/core';
import { format, parseISO } from 'date-fns';
import { httpVideo } from '../../util/http';

const columnsDefinition: MUIDataTableColumnDef[] = [
  {
    name: 'name',
    label: 'Nome',
  },
  {
    name: 'is_active',
    label: 'Ativo?',
    options: {
      customBodyRender(value, tableMeta, updateValue) {
        return value ? <Chip label="Sim" color="primary" /> : <Chip label="Não" color="secondary" />;
      },
    },
  },
  {
    name: 'created_at',
    label: 'Criado em',
    options: {
      customBodyRender(value, tableMeta, updateValue) {
        return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>;
      },
    },
  },
];

type Props = {};

// eslint-disable-next-line no-unused-vars
const Table = (props: Props) => {
  const [data, setData] = useState([]);

  useEffect(() => {
    async function loadCategories() {
      const response = await httpVideo.get('categories');
      setData(response.data.data);
    }

    loadCategories();
  }, []);

  return (
    <MUIDataTable
      title="teste"
      columns={columnsDefinition}
      data={data}
    />
  );
};

export default Table;
