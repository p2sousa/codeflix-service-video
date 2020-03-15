import * as React from 'react';
import MUIDataTable, { MUIDataTableColumnDef } from 'mui-datatables';
import { useEffect, useState } from 'react';
import { Chip } from '@material-ui/core';
import { format, parseISO } from 'date-fns';
import genreHttp from '../../util/http/genre-http';

const columnsDefinition: MUIDataTableColumnDef[] = [
  {
    name: 'name',
    label: 'Nome',
  },
  {
    name: 'categories',
    label: 'Categorias',
    options: {
      customBodyRender(value, tableMeta) {
        return value.map((category) => category.name).join(', ');
      },
    },
  },
  {
    name: 'is_active',
    label: 'Ativo?',
    options: {
      customBodyRender(value, tableMeta) {
        return value ? <Chip label="Sim" color="primary" /> : <Chip label="Não" color="secondary" />;
      },
    },
  },
  {
    name: 'created_at',
    label: 'Criado em',
    options: {
      customBodyRender(value, tableMeta) {
        return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>;
      },
    },
  },
];

interface Genre {
  id: string;
  name: string;
}

interface Props {}

const Table = (props: Props) => {
  const [data, setData] = useState<Genre[]>([]);

  useEffect(() => {
    async function loadGenres() {
      const response = await genreHttp.list<{data: Genre[]}>();
      setData(response.data.data);
    }

    loadGenres();
  }, []);

  return (
    <MUIDataTable
      title=""
      columns={columnsDefinition}
      data={data}
    />
  );
};

export default Table;
