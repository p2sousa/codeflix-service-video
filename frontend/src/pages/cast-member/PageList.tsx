import * as React from 'react';
import { Box, Fab } from '@material-ui/core';
import { Link } from 'react-router-dom';
import AddIcon from '@material-ui/icons/Add';
import { Page } from '../../components/Page';
import Table from './Table';

// eslint-disable-next-line @typescript-eslint/no-empty-interface
interface ListProps {}

// eslint-disable-next-line no-unused-vars
const PageList = (props: ListProps) => (
  <Page title="Listagem de membros de elencos">
    <Box dir="rtl">
      <Fab
        title="Adicionar membro de elenco"
        size="small"
        component={Link}
        to="/cast-members/create"
      >
        <AddIcon />
      </Fab>
    </Box>

    <Box>
      <Table />
    </Box>
  </Page>
);

export default PageList;
