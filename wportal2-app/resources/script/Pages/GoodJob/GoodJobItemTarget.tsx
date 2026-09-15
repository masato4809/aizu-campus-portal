import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnGoodJob } from '@/script/Models/App/Trn/TrnGoodJobList';
import { E_TARGET_TYPE } from '@/script/Enum/Server/App/GoodJob/ETargetType';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { TypoText } from '@/script/Component/Typography/TypoText';

export const E_TARGET = {
  FROM: 'from',
  TO: 'to',
} as const;
export type ETarget = (typeof E_TARGET)[keyof typeof E_TARGET];

interface IProps extends IPropsBase {
  target: ETarget;
  trnGoodJob: IAppTrnGoodJob;
}
export const GoodJobItemTarget: React.FC<IProps> = ({
  sx,
  target,
  trnGoodJob,
}) => {
  const type =
    target === E_TARGET.FROM
      ? trnGoodJob.fromTargetType
      : trnGoodJob.toTargetType;

  /**
   * 各タイプの表記
   */
  const nodeFrom = (): React.ReactNode => {
    switch (type) {
      case E_TARGET_TYPE.USER:
        const trnUser =
          target === E_TARGET.FROM
            ? trnGoodJob.fromTrnUser
            : trnGoodJob.toTrnUser;
        return <Avatar trnUser={trnUser} />;
      case E_TARGET_TYPE.DIVISION:
        const trnDivision =
          target === E_TARGET.FROM
            ? trnGoodJob.fromTrnDivision
            : trnGoodJob.toTrnDivision;
        return <TypoText>{trnDivision?.name}</TypoText>;
      case E_TARGET_TYPE.PROJECT:
        const trnProject =
          target === E_TARGET.FROM
            ? trnGoodJob.fromTrnProject
            : trnGoodJob.toTrnProject;
        return <TypoText>{trnProject?.name}</TypoText>;
      case E_TARGET_TYPE.LABEL:
        const label =
          target === E_TARGET.FROM
            ? trnGoodJob.fromOtherLabel
            : trnGoodJob.toOtherLabel;
        return <TypoText>{label}</TypoText>;
      default:
        return null;
    }
  };

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      {nodeFrom()}
    </Box>
  );
};
