import { Theme, useMediaQuery } from '@mui/material';

/**
 * MediaQuery相当のサイズ判定に利用する.
 */
export const useMediaSize = () => {
  const isMobileSize = useMediaQuery((theme: Theme) => {
    return theme.breakpoints.down('sm');
  });

  return { isMobileSize };
};
