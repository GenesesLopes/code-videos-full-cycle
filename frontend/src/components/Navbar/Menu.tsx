// @flow 
import * as React from 'react';
import { IconButton, MenuItem, Menu as MuiMenu } from '@material-ui/core';
import MenuIcon from '@material-ui/icons/Menu';
import routes, {MyRouteProps} from '../../routes';
import { Link } from 'react-router-dom';

const listRoutes = [
    'dashboard',
    'categories.list',
    'cast-members.list',
    'genres.list'
];

const MenuRoutes = routes.filter(route => listRoutes.includes(route.name));


export const Menu: React.FC = () => {
   
    const [anchorRel,setAnchorRel] = React.useState(null);
    const open = Boolean(anchorRel)
    const handleOpen =(event: any)=> setAnchorRel(event.currentTarget);
    const handleClose = () => setAnchorRel(null)
    return (
        <>
            <IconButton 
                color="inherit"
                edge="start"
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
                anchorEl={anchorRel}
                onClose={handleClose}
                anchorOrigin={{vertical: 'bottom', horizontal: 'center'}}
                transformOrigin={{vertical: 'top', horizontal: 'center'}}
                getContentAnchorEl={null}
            >
                {
                    listRoutes.map(
                        (routeName, key) => {
                           const route = MenuRoutes.find(route => route.name === routeName) as MyRouteProps
                           return (
                            <MenuItem 
                                key={key} 
                                component={Link}
                                to={route.path as string}
                                onClick={handleClose}
                            >
                                {route.label}
                            </MenuItem>
                           )
                        }
                    )
                }
            </MuiMenu>
        </>
    );
};