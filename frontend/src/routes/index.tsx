import { RouteProps } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import CategoryList from '../pages/category/PageList';
import GenreList from '../pages/genre/PageList';

export interface MyRouteProps extends RouteProps {
  name: string;
  label: string;
}

const routes: MyRouteProps[] = [
  {
    name: 'dashboard',
    label: 'Dashboard',
    path: '/',
    component: Dashboard,
    exact: true,
  },
  {
    name: 'categories.list',
    label: 'Listar categorias',
    path: '/categories',
    component: CategoryList,
    exact: true,
  },
  {
    name: 'categories.create',
    label: 'Adicionar categoria',
    path: '/categories/create',
    component: CategoryList,
    exact: true,
  },
  {
    name: 'genres.list',
    label: 'Listar gêneros',
    path: '/genres',
    component: GenreList,
    exact: true,
  },
  {
    name: 'genres.create',
    label: 'Adicionar gênero',
    path: '/genres/create',
    component: GenreList,
    exact: true,
  },
];

export default routes;
