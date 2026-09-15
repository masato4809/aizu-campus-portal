import * as React from 'react';
import { Box, Paper } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnUserReward } from '@/script/Models/App/Trn/TrnUserRewardList';
import { E_COLOR } from '@/script/Enum/EColor';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { RewardGold } from '@/script/Pages/Reward/Common/RewardGold';
import { RewardTitle } from '@/script/Pages/Reward/Common/RewardTitle';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { TypoText2 } from '@/script/Component/Typography/TypoText2';
import { RewardImage } from '@/script/Pages/Reward/Common/RewardImage';
import { E_PAGES, getPagesHref } from '@/script/Enum/Server/App/EPages';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { Closable } from '@/script/Component/Misc/Closable';
import { TypoText } from '@/script/Component/Typography/TypoText';

interface IProps extends IPropsBase {
  trnUserReward: IAppTrnUserReward;
}
export const Achievement: React.FC<IProps> = ({ sx, trnUserReward }) => {
  const { onStart, onFinish } = useProgressContext();

  /**
   * 報酬獲得.
   */
  const handleSubmitArchive = () => {
    // 送信処理.
    router.post(
      getPagesHref(E_PAGES.REWARD__ACHIEVE),
      {
        trnUserRewardId: trnUserReward.id,
      },
      {
        onStart,
        onFinish,
        preserveState: false,
        preserveScroll: false,
      },
    );
  };

  /**
   * 受け取り可能回数.
   */
  const enableReceiveCount = Math.max(
    trnUserReward.achievementCount - trnUserReward.receivedCount,
  );

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
      <RewardImage mstReward={trnUserReward.mstReward} />
      <Box
        sx={{
          flexGrow: 1,
          display: 'flex',
          flexDirection: 'column',
        }}
      >
        <RewardTitle
          mstReward={trnUserReward.mstReward}
          achievementCount={trnUserReward.achievementCount}
        />
        <TypoText2
          sx={{
            whiteSpace: 'pre-wrap',
            marginBottom: responsiveSpacing(2),
          }}
        >
          {trnUserReward.mstReward?.rewardExplain}
        </TypoText2>
        <Box
          sx={{
            marginTop: 'auto',
          }}
        >
          <RewardGold gold={Number(trnUserReward.mstReward?.rewardGold)} />
        </Box>
      </Box>

      <Box
        sx={{
          width: responsiveSize(120),
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
        }}
      >
        <Closable open={!!enableReceiveCount}>
          <ButtonGeneral
            sx={{
              py: responsiveSize(6),
              px: responsiveSize(4),
              minWidth: 0,
            }}
            label={`${enableReceiveCount}回受取`}
            onClick={handleSubmitArchive}
          />
        </Closable>
        <Closable open={!enableReceiveCount}>
          <TypoText bold color={E_COLOR.TEXT_GREEN}>
            受取済
          </TypoText>
        </Closable>
      </Box>
    </Paper>
  );
};
