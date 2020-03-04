import * as React from 'react';
import MUIDataTable, { MUIDataTableColumnDef } from 'mui-datatables';
import { useEffect, useState } from 'react';
import { Chip } from '@material-ui/core';
import { format, parseISO } from 'date-fns';
import castMemberHttp from '../../util/http/cast-meber-http';

const CastMemberTypeMap = {
  1: 'Diretor',
  2: 'Ator',
};

const columnsDefinition: MUIDataTableColumnDef[] = [
  {
    name: 'name',
    label: 'Nome',
  },
  {
    name: 'type',
    label: 'Tipo',
    options: {
      customBodyRender(value, tableMeta) {
        return CastMemberTypeMap[value];
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

interface CastMember {
  id: string;
  name: string;
}

interface Props {}

const Table = (props: Props) => {
  const [data, setData] = useState<CastMember[]>([]);

  useEffect(() => {
    async function loadCastMembers() {
      const response = await castMemberHttp.list<{data: CastMember[]}>();
      setData(response.data.data);
    }

    loadCastMembers();
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
