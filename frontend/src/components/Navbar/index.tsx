import * as React from 'react';
import { AppBar, Button, IconButton, makeStyles, Theme, Menu, Toolbar, Typography, MenuItem } from '@material-ui/core';
import MenuIcon from '@material-ui/icons/Menu';
import logo from '../../assets/img/logo.png';

const useStyles = makeStyles((theme: Theme) => ({
    toolbar: {
        backgroundColor: '#000000'
    },
    title: {
        flexGrow: 1,
        textAlign: 'center'
    },
    logo: {
        width: 100,
        [theme.breakpoints.up('sm')]:{
            width: 170
        }
    }
}))

export const Navbar: React.FC = () => {
    const classes = useStyles()
    return (
       <AppBar>
           <Toolbar className={classes.toolbar}>
            <IconButton 
                color="inherit"
                edge="start"
                aria-label="open drawer"
                aria-controls="menu-appbar"
                aria-haspopup="true"
            >
                <MenuIcon />
            </IconButton>
            <Menu 
                id="menu-appbar"
                open={false}
            >
                <MenuItem>Categorias</MenuItem>
            </Menu>
            <Typography className={classes.title}>
                <img src={logo} alt="Codeflix" className={classes.logo}/>
            </Typography>
            <Button color='inherit'>Login</Button>
           </Toolbar>
        </AppBar>
    );
};