import * as React from 'react';
import { IconButton, Menu as MuiMenu, MenuItem } from '@material-ui/core';
import MenuIcon from '@material-ui/icons/Menu';
import { Link } from 'react-router-dom';
import routes, { MyRouteProps } from '../../routes';

const listRoutes = [
  'dashboard',
  'categories.list',
  'genres.list',
  'cast-members.list',
];
const menuRoutes = routes.filter((route) => listRoutes.includes(route.name));

export const Menu = () => {
  const [anchorEl, setAnchorEl] = React.useState(null);
  const open = Boolean(anchorEl);
  const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
  const handleClose = () => setAnchorEl(null);

  return (
    <>
      <IconButton
        edge="start"
        color="inherit"
        aria-label="open drawer"
        aria-controls="menu-appbar"
        aria-haspopup="true"
        onClick={handleOpen}
      >
        <MenuIcon />
      </IconButton>

      <MuiMenu
        id="menu-appbar"
        open={open}
        anchorEl={anchorEl}
        onClose={handleClose}
        anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
        transformOrigin={{ vertical: 'top', horizontal: 'center' }}
        getContentAnchorEl={null}
      >
        {
          listRoutes.map(
            (routeName, key) => {
              const route = menuRoutes.find((router) => router.name === routeName) as MyRouteProps;
              return (
                <MenuItem
                  key={key}
                  component={Link}
                  to={route.path as string}
                  onClick={handleClose}
                >
                  {route.label}
                </MenuItem>
              );
            },
          )
        }
      </MuiMenu>
    </>
  );
};
