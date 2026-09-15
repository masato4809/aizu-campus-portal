import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IAppMstReward } from '@/script/Models/App/Mst/MstRewardList';
import { E_COLOR } from '@/script/Enum/EColor';
import { responsiveSpacing } from '@/script/System/Responsive';
import { RewardImage } from '@/script/Pages/Reward/Common/RewardImage';
import { RewardTitle } from '@/script/Pages/Reward/Common/RewardTitle';
import { TypoText2 } from '@/script/Component/Typography/TypoText2';
import { RewardGold } from '@/script/Pages/Reward/Common/RewardGold';

interface IProps extends IPropsBase {
  mstReward: IAppMstReward;
}
export const MstReward: React.FC<IProps> = ({ sx, mstReward }) => {
  return (
    <Paper
      sx={{
        border: `1px solid ${E_COLOR.GREY}`,
        display: 'flex',
        padding: responsiveSpacing(2),
        alignItems: 'stretch',
        gap: responsiveSpacing(2),
        ...sx,
      }}
    >
      <RewardImage mstReward={mstReward} />
      <Box
        sx={{
          flexGrow: 1,
          display: 'flex',
          flexDirection: 'column',
        }}
      >
        <RewardTitle mstReward={mstReward} achievementCount={0} />
        <TypoText2
          sx={{
            whiteSpace: 'pre-wrap',
            marginBottom: responsiveSpacing(2),
          }}
        >
          {mstReward.rewardExplain}
        </TypoText2>
        <Box
          sx={{
            marginTop: 'auto',
          }}
        >
          <RewardGold gold={mstReward.rewardGold} />
        </Box>
      </Box>
    </Paper>
  );
};
