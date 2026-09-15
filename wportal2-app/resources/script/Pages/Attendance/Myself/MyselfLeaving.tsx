import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { History } from '@/script/Pages/Attendance/Myself/History';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const MyselfLeaving: React.FC<IProps> = ({ sx }) => {
  return (
    <>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'center',
          ...sx,
        }}
      >
        <Box
          sx={{
            padding: responsiveSpacing(4),
            borderRadius: responsiveSpacing(4),
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            background: E_COLOR.PRIMARY_LIGHT,
            ...sx,
          }}
        >
          <TypoText>本日は退勤しました</TypoText>
        </Box>
      </Box>
      <History
        sx={{
          marginTop: responsiveSpacing(4),
        }}
      />
    </>
  );
};
