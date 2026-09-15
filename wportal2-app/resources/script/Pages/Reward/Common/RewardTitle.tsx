import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { responsiveSpacing } from '@/script/System/Responsive';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR } from '@/script/Enum/EColor';
import { Closable } from '@/script/Component/Misc/Closable';
import { IAppMstReward } from '@/script/Models/App/Mst/MstRewardList';

interface IProps extends IPropsBase {
  mstReward?: IAppMstReward;
  achievementCount: number;
}
export const RewardTitle: React.FC<IProps> = ({
  sx,
  mstReward,
  achievementCount,
}) => {
  return (
    <Box
      sx={{
        backgroundColor: E_COLOR.PRIMARY_LIGHT,
        display: 'flex',
        gap: responsiveSpacing(2),
        ...sx,
      }}
    >
      <TypoText>{`No.${mstReward?.id}`}</TypoText>
      <TypoText>{mstReward?.rewardName}</TypoText>
      <Closable open={!!achievementCount}>
        <TypoText sx={{ marginLeft: 'auto' }} bold>
          {`[${achievementCount}回達成]`}
        </TypoText>
      </Closable>
    </Box>
  );
};
