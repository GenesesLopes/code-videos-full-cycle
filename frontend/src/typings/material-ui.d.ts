import { ComponentNameToClassKey } from '@material-ui/core/styles/overrides'
import { PaletteOptions, PaletteColor } from "@material-ui/core/styles/createPalette";


declare module '@material-ui/core/styles/overrides' {
    interface ComponentNameToClassKey {
        MUIDataTable: any;
        MUIDataTableToolbar: any;
        MUIDataTableHeadCell: any;
        MuiTableSortLabel: any;
        MUIDataTableSelectCell: any;
        MUIDataTableSelectBodyRow: any;
        MUIDataTablePagination: any;
    }
}

declare module '@material-ui/core/styles/createPalette' {
    import {SimplePaletteColorOptions} from '@material-ui/core/styles'
    interface Palette {
        success: PaletteColor
    }
    interface PaletteOptions {
        success?: SimplePaletteColorOptions
    }
}