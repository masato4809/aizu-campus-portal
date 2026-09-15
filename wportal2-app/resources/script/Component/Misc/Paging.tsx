import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { TypoText } from '@/script/Component/Typography/TypoText';

interface IProps extends IPropsBase {
  index: number;
  step: number;
  total: number;
  handlePrev: () => void;
  handleNext: () => void;
}
export const Paging: React.FC<IProps> = ({
  sx,
  index,
  step,
  total,
  handlePrev,
  handleNext,
}) => {
  // ページの開始番号と終了番号.
  const start = index * step + 1;
  const end = Math.min((index + 1) * step, total);

  // 前ページに戻れるかどうか.
  const isEnablePrev = index > 0;

  // 次ページに進めるかどうか.
  const isEnableNext = (index + 1) * step < total;

  return (
    <Box
      sx={{
        display: 'flex',
        alignItems: 'center',
        gap: '10px',
        ...sx,
      }}
    >
      <TypoText>{`${start}～${end} / ${total}件中`}</TypoText>
      <ButtonGeneral
        sx={{
          padding: '5px 10px',
        }}
        label={`前の${step}件`}
        onClick={handlePrev}
        disabled={!isEnablePrev}
      />
      <ButtonGeneral
        sx={{
          padding: '5px 10px',
        }}
        label={`次の${step}件`}
        onClick={handleNext}
        disabled={!isEnableNext}
      />
    </Box>
  );
};
