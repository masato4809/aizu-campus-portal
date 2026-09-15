import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { Box, Paper } from '@mui/material';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { router } from '@inertiajs/react';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

interface IProps extends IPropsBase {}
export const Calculating: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();

  /**
   * リロードボタンを押した.
   */
  const handleClickReload = () => {
    router.reload({
      method: 'get',
      onStart,
      onFinish,
    });
  };

  return (
    <Paper
      sx={{
        marginTop: responsiveSpacing(4),
        padding: responsiveSpacing(4),
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
      }}
    >
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'center',
        }}
      >
        <Box
          sx={{
            padding: responsiveSpacing(4),
            borderRadius: responsiveSpacing(4),
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            background: E_COLOR.PRIMARY_LIGHT,
          }}
        >
          <TypoH2>現在マッチング処理中です</TypoH2>
        </Box>
      </Box>
      <ButtonGeneral
        sx={{
          marginTop: responsiveSpacing(4),
          width: responsiveSize(200),
        }}
        label="リロードする"
        onClick={handleClickReload}
      />
    </Paper>
  );
};
