import * as React from 'react';

import { IPropsBase } from '@/script/System/System';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { DateTime } from '@/script/Common/DateTime';
import { Box, Divider, Paper } from '@mui/material';
import {
  E_TARGET,
  GoodJobItemTarget,
} from '@/script/Pages/GoodJob/GoodJobItemTarget';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { IAppTrnGoodJob } from '@/script/Models/App/Trn/TrnGoodJobList';
import { TypoH3 } from '@/script/Component/Typography/TypoH3';

interface IProps extends IPropsBase {
  trnGoodJob: IAppTrnGoodJob;
}
export const GoodJobItem: React.FC<IProps> = ({ trnGoodJob, sx }) => {
  return (
    <Paper
      sx={{
        padding: responsiveSpacing(4),
        '&:not(:first-of-type)': {
          marginTop: responsiveSpacing(2),
        },
        ...sx,
      }}
    >
      <TypoText>
        {DateTime.parseString(trnGoodJob.createdAt).toDateTime()}
      </TypoText>
      <Box
        sx={{
          marginTop: responsiveSpacing(2),
          display: 'flex',
          alignItems: 'center',
          gap: responsiveSpacing(4),
        }}
      >
        <GoodJobItemTarget target={E_TARGET.FROM} trnGoodJob={trnGoodJob} />
        <Icon icon={E_ICON.ARROW_RIGHT} />
        <GoodJobItemTarget target={E_TARGET.TO} trnGoodJob={trnGoodJob} />
      </Box>
      <TypoH3
        sx={{
          marginTop: responsiveSpacing(2),
        }}
      >
        {trnGoodJob.title}
      </TypoH3>
      <Divider sx={{ marginTop: responsiveSize(5) }} />
      <TypoText
        sx={{
          marginTop: responsiveSpacing(2),
          whiteSpace: 'pre-wrap',
        }}
      >
        {trnGoodJob.content}
      </TypoText>
    </Paper>
  );
};
