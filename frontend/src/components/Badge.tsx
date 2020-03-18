import * as React from 'react';
import { Chip, createMuiTheme, MuiThemeProvider } from '@material-ui/core';
import theme from '../theme';

const badgeTheme = createMuiTheme({
  palette: {
    primary: theme.palette.success,
    secondary: theme.palette.error,
  },
});

export const BadgeYes = () => (
  <MuiThemeProvider theme={badgeTheme}>
    <Chip label="Sim" color="primary" />
  </MuiThemeProvider>
);

export const BadgeNo = () => (
  <MuiThemeProvider theme={badgeTheme}>
    <Chip label="Não" color="secondary" />
  </MuiThemeProvider>
);
