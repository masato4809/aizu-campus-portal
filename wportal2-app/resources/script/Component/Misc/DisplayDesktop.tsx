import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';

/**
 * styledに切り出した場合の書き方.
 */
// const DisplaySwitchBox = styled(Box)((props) => ({
//   [`&.${boxClasses.root}`]: {
//     [props.theme.breakpoints.down('md')]: {
//       display: 'none',
//     }
//   }
// }))

/**
 * レスポンシブル対応
 * 特定のウィンドウサイズにおいて下位コンポーネントを非表示（display:none）とする
 * 描画はされないが要素としては存在する点に注意する.
 */
interface IProps extends IPropsBase {}
export const DisplayDesktop: React.FC<IProps> = ({ children }) => {
  return (
    <Box
      sx={{
        display: {
          xs: 'none',
          sm: 'none',
          md: 'block',
          lg: 'block',
          xl: 'block',
        },
      }}
    >
      {children}
    </Box>
  );
};
