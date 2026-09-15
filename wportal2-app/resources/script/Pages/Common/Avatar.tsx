import * as React from 'react';
import { Box, Chip, Avatar as MuiAvatar } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { E_COLOR, EColor } from '@/script/Enum/EColor';
import { Closable } from '@/script/Component/Misc/Closable';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';
import { responsiveSize } from '@/script/System/Responsive';

interface IProps extends IPropsBase {
  trnUser?: IAppTrnUser;
  active?: boolean;
  rest?: boolean;
  handleSelect?: (trnUser?: IAppTrnUser) => void;
  handleClose?: (trnUser: IAppTrnUser) => void;
}
export const Avatar: React.FC<IProps> = ({
  sx,
  trnUser,
  active = true,
  rest,
  handleClose,
  handleSelect,
}) => {
  /**
   * 表示カラーを取得.
   */
  const getColor = (): EColor => {
    if (rest) {
      return E_COLOR.GREY;
    }
    return active ? E_COLOR.GREEN_LIGHT : E_COLOR.GREY;
  };

  /**
   * 選択時の処理指定があれば.
   */
  const onClick = (_event: React.MouseEvent<HTMLDivElement>) => {
    if (handleSelect) {
      handleSelect(trnUser);
    }
  };

  /**
   * 画像表示.
   */
  const nodeAvatar = () => {
    return <MuiAvatar alt={trnUser?.nickname} src={trnUser?.faceImagePath} />;
  };

  /**
   * 名前表示.
   */
  const nodeLabel = (): React.ReactNode => {
    return (
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
          gap: responsiveSize(6),
        }}
      >
        <TypoText>{trnUser?.nickname}</TypoText>
        <Closable open={!!rest}>
          <Icon icon={E_ICON.CAFE} />
        </Closable>
        <Closable open={!!handleClose}>
          <Box
            sx={{
              cursor: 'pointer',
              borderLeft: `1px solid ${E_COLOR.GREY}`,
              paddingLeft: responsiveSize(6),
            }}
            onClick={() =>
              handleClose ? handleClose(trnUser as IAppTrnUser) : undefined
            }
          >
            <TypoText>×</TypoText>
          </Box>
        </Closable>
      </Box>
    );
  };

  return (
    <Chip
      sx={{
        backgroundColor: getColor(),
        height: responsiveSize(36),
        borderRadius: responsiveSize(18),
        '& .MuiChip-avatar': {
          width: responsiveSize(32),
          height: responsiveSize(32),
        },
        ...sx,
      }}
      onClick={onClick}
      avatar={nodeAvatar()}
      label={nodeLabel()}
    />
  );
};
