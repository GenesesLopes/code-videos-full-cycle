import { Box, Container, makeStyles, Typography } from '@material-ui/core';
import * as React from 'react';

const useStyles = makeStyles({
    title: {
        color: '#999999'
    }
})

type PageProps = {
    title: string
};

const index: React.FC<PageProps> = ({title, children}) => {
    // eslint-disable-next-line react-hooks/rules-of-hooks
    const classes = useStyles();

    return (
        <Container>
            <Typography className={classes.title} component='h1' variant='h5'>
                {title}
            </Typography>
            <Box paddingTop={2}>
                {children}
            </Box>
        </Container>
    );
};

export default index

