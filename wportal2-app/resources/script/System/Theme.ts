import { createTheme } from '@mui/material';
import { E_COLOR } from '@/script/Enum/EColor';

export const themeCommon = createTheme({
  palette: {
    mode: 'light',
    primary: {
      main: E_COLOR.PRIMARY_MAIN,
      light: E_COLOR.PRIMARY_LIGHT,
      dark: E_COLOR.PRIMARY_DARK,
      contrastText: E_COLOR.PRIMARY_TEXT,
    },
    secondary: {
      main: E_COLOR.SECONDARY_MAIN,
      light: E_COLOR.SECONDARY_LIGHT,
      dark: E_COLOR.SECONDARY_DARK,
      contrastText: E_COLOR.SECONDARY_TEXT,
    },
    background: {
      default: E_COLOR.BACKGROUND,
    },
    text: {
      primary: E_COLOR.TEXT_PRIMARY,
    },
  },
  spacing: 6,
  typography: {
    fontFamily: "'Noto Sans JP', sans-serif;",
    h1: {
      fontSize: '28px',
    },
    h2: {
      fontSize: '20px',
    },
    h3: {
      fontSize: '18px',
    },
    subtitle1: {
      fontSize: '18px',
    },
    subtitle2: {
      fontSize: '16px',
    },
    body1: {
      fontSize: '16px',
    },
    body2: {
      fontSize: '14px',
    },
    button: {
      fontSize: '16px',
    },
  },
  mixins: {
    toolbar: {
      height: '52px',
      padding: '0px',
    },
    drawer: {
      width: '240px',
    },
  },
  breakpoints: {
    values: {
      xs: 0,
      sm: 375,
      md: 800,
      lg: 1024,
      xl: 1280,
    },
  },
  components: {
    MuiTypography: {
      defaultProps: {
        noWrap: true,
      },
    },
    MuiTextField: {
      defaultProps: {
        variant: 'outlined',
      },
    },
    MuiSelect: {
      defaultProps: {
        variant: 'outlined',
      },
    },
    MuiButton: {
      defaultProps: {
        variant: 'contained',
        color: 'primary',
      },
      styleOverrides: {
        startIcon: {
          alignItems: 'center',
        },
      },
    },
  },
});
