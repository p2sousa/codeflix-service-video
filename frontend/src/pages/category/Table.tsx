import * as React from 'react';
import MUIDataTable, { MUIDataTableColumnDef } from 'mui-datatables';
import { useEffect, useState } from 'react';
import { Chip } from '@material-ui/core';
import { format, parseISO } from 'date-fns';
import categoryHttp from '../../util/http/category-http';

const columnsDefinition: MUIDataTableColumnDef[] = [
  {
    name: 'name',
    label: 'Nome',
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

interface Category {
  id: string;
  name: string;
}

type Props = {};

const Table = (props: Props) => {
  const [data, setData] = useState<Category[]>([]);

  useEffect(() => {
    async function loadCategories() {
      const response = await categoryHttp.list<{data: Category[]}>();
      setData(response.data.data);
    }

    loadCategories();
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
