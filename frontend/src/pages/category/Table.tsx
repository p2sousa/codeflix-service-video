import * as React from 'react';
import MUIDataTable, { MUIDataTableColumnDef } from 'mui-datatables';
import { useEffect, useState } from 'react';
import { httpVideo } from '../../util/http';

const columnsDefinition: MUIDataTableColumnDef[] = [
  {
    name: 'name',
    label: 'Nome',
  },
  {
    name: 'is_active',
    label: 'Ativo?',
  },
  {
    name: 'created_at',
    label: 'Criado em',
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
