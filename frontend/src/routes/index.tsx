import {RouteProps} from 'react-router-dom';
import CategoryList from '../pages/category/PageList';
import CategoryCreate from '../pages/category/PageForm';
import Dashboard from '../pages/Dashboard';
import CastMemberList from '../pages/cast_member/PageList';
import CastMemberCreate from '../pages/cast_member/PageForm';
import GenreList from '../pages/genres/PageList';

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
        exact: true
    },
    {
        name: 'categories.list',
        label: 'Listar categorias',
        path: '/categories',
        component: CategoryList,
        exact: true
    },
    {
        name: 'categories.create',
        label: 'Criar categorias',
        path: '/categories/create',
        component: CategoryCreate,
        exact: true
    },
    {
        name: 'cast-members.list',
        label: 'Listar membros da equipe',
        path: '/cast-members',
        component: CastMemberList,
        exact: true
    },
    {
        name: 'cast-members.create',
        label: 'Criar membros da equipe',
        path: '/cast-members/create',
        component: CastMemberCreate,
        exact: true
    },
    {
        name: 'genres.list',
        label: 'Listar generos',
        path: '/genres',
        component: GenreList,
        exact: true
    },
    {
        name: 'genres.create',
        label: 'Criar generos',
        path: '/genres/create',
        component: GenreList,
        exact: true
    }
];

export default routes;